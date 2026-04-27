<?php

namespace App\Services;

use App\Models\MpesaDeposit;
use App\Models\SavingsGoal;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Notifications\FundsLockedNotification;
use App\Notifications\WalletWithdrawalNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class WalletService
{
    public function getOrCreateWallet(int $userId): Wallet
    {
        return Wallet::firstOrCreate(
            ['user_id' => $userId],
            [
                'currency' => 'KES',
                'available_balance' => 0,
                'locked_balance' => 0,
                'status' => 'active',
            ]
        );
    }

    public function createGoal(
        int $userId,
        string $name,
        ?string $description,
        string $lockType,
        ?float $targetAmount,
        ?string $lockUntil,
        bool $allowPartialWithdrawal
    ): SavingsGoal {
        $wallet = $this->getOrCreateWallet($userId);

        return SavingsGoal::create([
            'wallet_id' => $wallet->id,
            'user_id' => $userId,
            'name' => $name,
            'description' => $description,
            'target_amount' => $targetAmount,
            'current_amount' => 0,
            'lock_type' => $lockType,
            'lock_until' => $lockUntil,
            'allow_partial_withdrawal' => $allowPartialWithdrawal,
            'status' => 'active',
        ]);
    }

    public function creditDeposit(
        int $userId,
        float $amount,
        ?string $reference = null,
        ?string $externalReference = null,
        ?array $meta = null
    ): WalletTransaction {
        return DB::transaction(function () use ($userId, $amount, $reference, $externalReference, $meta) {
            $wallet = Wallet::where('user_id', $userId)->lockForUpdate()->first();

            if (! $wallet) {
                $wallet = $this->getOrCreateWallet($userId)->fresh();
                $wallet = Wallet::whereKey($wallet->id)->lockForUpdate()->firstOrFail();
            }

            $wallet->available_balance = (float) $wallet->available_balance + $amount;
            $wallet->last_activity_at = now();
            $wallet->save();

            return WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $userId,
                'type' => 'deposit',
                'status' => 'completed',
                'amount' => $amount,
                'available_balance_after' => $wallet->available_balance,
                'locked_balance_after' => $wallet->locked_balance,
                'reference' => $reference,
                'external_reference' => $externalReference,
                'source' => 'mpesa',
                'meta' => $meta,
                'processed_at' => now(),
            ]);
        });
    }

    public function lockFundsToGoal(int $userId, int $goalId, float $amount): void
    {
        DB::transaction(function () use ($userId, $goalId, $amount) {
            $wallet = Wallet::where('user_id', $userId)->lockForUpdate()->firstOrFail();
            $goal = SavingsGoal::where('user_id', $userId)->whereKey($goalId)->lockForUpdate()->firstOrFail();

            if ($amount <= 0) {
                throw new RuntimeException('Amount must be greater than zero.');
            }

            if ((float) $wallet->available_balance < $amount) {
                throw new RuntimeException('Insufficient available balance.');
            }

            $wallet->available_balance = (float) $wallet->available_balance - $amount;
            $wallet->locked_balance = (float) $wallet->locked_balance + $amount;
            $wallet->last_activity_at = now();
            $wallet->save();

            $goal->current_amount = (float) $goal->current_amount + $amount;
            $goal->save();

            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $userId,
                'type' => 'lock',
                'status' => 'completed',
                'amount' => $amount,
                'available_balance_after' => $wallet->available_balance,
                'locked_balance_after' => $wallet->locked_balance,
                'source' => 'user',
                'related_type' => SavingsGoal::class,
                'related_id' => $goal->id,
                'processed_at' => now(),
            ]);

            DB::afterCommit(function () use ($userId, $amount, $goal): void {
                $user = User::find($userId);

                if (! $user) {
                    return;
                }

                try {
                    $user->notify(new FundsLockedNotification(
                        $amount,
                        (string) $goal->lock_type,
                        (string) $goal->name
                    ));
                } catch (Throwable $e) {
                    $context = [
                        'user_id' => $userId,
                        'amount' => $amount,
                        'goal_id' => $goal->id,
                        'goal_name' => $goal->name,
                        'exception' => $e::class,
                        'message' => $e->getMessage(),
                    ];

                    Log::error('Failed to send funds locked notification email.', $context);
                    Log::channel('stderr')->error('Failed to send funds locked notification email.', $context);
                    report($e);
                }
            });
        });
    }

    public function withdrawFromAvailable(int $userId, float $amount): void
    {
        DB::transaction(function () use ($userId, $amount) {
            $wallet = Wallet::where('user_id', $userId)->lockForUpdate()->firstOrFail();

            if ($amount <= 0) {
                throw new RuntimeException('Amount must be greater than zero.');
            }

            if ((float) $wallet->available_balance < $amount) {
                throw new RuntimeException('Insufficient available balance.');
            }

            $wallet->available_balance = (float) $wallet->available_balance - $amount;
            $wallet->last_activity_at = now();
            $wallet->save();

            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $userId,
                'type' => 'withdrawal',
                'status' => 'completed',
                'amount' => $amount,
                'available_balance_after' => $wallet->available_balance,
                'locked_balance_after' => $wallet->locked_balance,
                'source' => 'user',
                'processed_at' => now(),
            ]);

            DB::afterCommit(function () use ($userId, $amount): void {
                $user = User::find($userId);

                if (! $user) {
                    return;
                }

                try {
                    $user->notify(new WalletWithdrawalNotification(
                        $amount,
                        'available balance'
                    ));
                } catch (Throwable $e) {
                    $context = [
                        'user_id' => $userId,
                        'amount' => $amount,
                        'exception' => $e::class,
                        'message' => $e->getMessage(),
                    ];

                    Log::error('Failed to send available balance withdrawal notification email.', $context);
                    Log::channel('stderr')->error('Failed to send available balance withdrawal notification email.', $context);
                    report($e);
                }
            });
        });
    }

    public function withdrawFromGoal(int $userId, int $goalId, float $amount): void
    {
        DB::transaction(function () use ($userId, $goalId, $amount) {
            $wallet = Wallet::where('user_id', $userId)->lockForUpdate()->firstOrFail();
            $goal = SavingsGoal::where('user_id', $userId)->whereKey($goalId)->lockForUpdate()->firstOrFail();

            if (! $goal->isUnlocked()) {
                throw new RuntimeException('Selected goal is still locked.');
            }

            if ($amount <= 0) {
                throw new RuntimeException('Amount must be greater than zero.');
            }

            if ((float) $goal->current_amount < $amount) {
                throw new RuntimeException('Amount exceeds goal balance.');
            }

            $goal->current_amount = (float) $goal->current_amount - $amount;
            $goal->save();

            $wallet->available_balance = (float) $wallet->available_balance + $amount;
            $wallet->locked_balance = (float) $wallet->locked_balance - $amount;
            $wallet->last_activity_at = now();
            $wallet->save();

            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $userId,
                'type' => 'withdrawal',
                'status' => 'completed',
                'amount' => $amount,
                'available_balance_after' => $wallet->available_balance,
                'locked_balance_after' => $wallet->locked_balance,
                'source' => 'user',
                'related_type' => SavingsGoal::class,
                'related_id' => $goal->id,
                'processed_at' => now(),
            ]);

            DB::afterCommit(function () use ($userId, $amount, $goal): void {
                $user = User::find($userId);

                if (! $user) {
                    return;
                }

                try {
                    $user->notify(new WalletWithdrawalNotification(
                        $amount,
                        'savings goal: ' . $goal->name
                    ));
                } catch (Throwable $e) {
                    $context = [
                        'user_id' => $userId,
                        'amount' => $amount,
                        'goal_id' => $goal->id,
                        'goal_name' => $goal->name,
                        'exception' => $e::class,
                        'message' => $e->getMessage(),
                    ];

                    Log::error('Failed to send savings goal withdrawal notification email.', $context);
                    Log::channel('stderr')->error('Failed to send savings goal withdrawal notification email.', $context);
                    report($e);
                }
            });
        });
    }

    public function createInitiatedMpesaDeposit(
        int $userId,
        string $phoneNumber,
        float $amount,
        ?array $requestPayload = null
    ): MpesaDeposit {
        $wallet = $this->getOrCreateWallet($userId);

        return MpesaDeposit::create([
            'wallet_id' => $wallet->id,
            'user_id' => $userId,
            'phone_number' => $phoneNumber,
            'amount' => $amount,
            'status' => 'initiated',
            'request_payload' => $requestPayload,
        ]);
    }
} 