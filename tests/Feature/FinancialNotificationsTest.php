<?php

namespace Tests\Feature;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Expense;
use App\Models\SavingsGoal;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\BudgetLimitExceededNotification;
use App\Notifications\FundsLockedNotification;
use App\Notifications\WalletWithdrawalNotification;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class FinancialNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_funds_locked_sends_notification(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $wallet = Wallet::create([
            'user_id' => $user->id,
            'currency' => 'KES',
            'available_balance' => 500,
            'locked_balance' => 0,
            'status' => 'active',
        ]);

        $goal = SavingsGoal::create([
            'wallet_id' => $wallet->id,
            'user_id' => $user->id,
            'name' => 'Emergency Fund',
            'description' => null,
            'target_amount' => 1000,
            'current_amount' => 0,
            'lock_type' => 'amount',
            'lock_until' => null,
            'allow_partial_withdrawal' => true,
            'status' => 'active',
        ]);

        app(WalletService::class)->lockFundsToGoal($user->id, $goal->id, 200);

        Notification::assertSentTo($user, FundsLockedNotification::class);
    }

    public function test_wallet_withdrawal_sends_notification(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        Wallet::create([
            'user_id' => $user->id,
            'currency' => 'KES',
            'available_balance' => 500,
            'locked_balance' => 0,
            'status' => 'active',
        ]);

        app(WalletService::class)->withdrawFromAvailable($user->id, 150);

        Notification::assertSentTo($user, WalletWithdrawalNotification::class);
    }

    public function test_withdrawing_from_goal_moves_money_back_to_available_balance(): void
    {
        $user = User::factory()->create();

        $wallet = Wallet::create([
            'user_id' => $user->id,
            'currency' => 'KES',
            'available_balance' => 100,
            'locked_balance' => 200,
            'status' => 'active',
        ]);

        $goal = SavingsGoal::create([
            'wallet_id' => $wallet->id,
            'user_id' => $user->id,
            'name' => 'Travel Fund',
            'description' => null,
            'target_amount' => 200,
            'current_amount' => 200,
            'lock_type' => 'amount',
            'lock_until' => null,
            'allow_partial_withdrawal' => true,
            'status' => 'active',
        ]);

        app(WalletService::class)->withdrawFromGoal($user->id, $goal->id, 50);

        $wallet->refresh();
        $goal->refresh();

        $this->assertSame('150.00', (string) $wallet->available_balance);
        $this->assertSame('150.00', (string) $wallet->locked_balance);
        $this->assertSame('150.00', (string) $goal->current_amount);
    }

    public function test_budget_limit_notification_is_sent_when_category_budget_is_crossed(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $category = Category::create([
            'user_id' => $user->id,
            'name' => 'Food',
            'color' => '#22c55e',
        ]);

        Budget::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 100,
            'month' => now()->month,
            'year' => now()->year,
        ]);

        Expense::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 60,
            'title' => 'First grocery run',
            'description' => null,
            'date' => now()->toDateString(),
            'type' => 'one-time',
        ]);

        Notification::assertNothingSent();

        Expense::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 50,
            'title' => 'Second grocery run',
            'description' => null,
            'date' => now()->toDateString(),
            'type' => 'one-time',
        ]);

        Notification::assertSentTo($user, BudgetLimitExceededNotification::class);
    }

    public function test_budget_limit_notification_supports_uncategorized_misc_expenses(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        Budget::create([
            'user_id' => $user->id,
            'category_id' => null,
            'amount' => 80,
            'month' => now()->month,
            'year' => now()->year,
        ]);

        Expense::create([
            'user_id' => $user->id,
            'category_id' => null,
            'amount' => 45,
            'title' => 'Misc expense A',
            'description' => null,
            'date' => now()->toDateString(),
            'type' => 'one-time',
        ]);

        Notification::assertNothingSent();

        Expense::create([
            'user_id' => $user->id,
            'category_id' => null,
            'amount' => 40,
            'title' => 'Misc expense B',
            'description' => null,
            'date' => now()->toDateString(),
            'type' => 'one-time',
        ]);

        Notification::assertSentTo($user, BudgetLimitExceededNotification::class);
    }
}
