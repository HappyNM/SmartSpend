<?php

namespace App\Livewire\Wallet;

use App\Models\SavingsGoal;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Services\WalletService;
use RuntimeException;

#[Title('Withdraw Funds - ExpenseApp')]
class WithdrawFunds extends Component
{
    public string $amount = '';
    public string $source = 'available';
    public string $goal_id = '';

    protected function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:1'],
            'source' => ['required', 'in:available,goal'],
            'goal_id' => ['nullable'],
        ];
    }

    #[Computed]
    public function wallet(): Wallet
    {
        return Wallet::firstOrCreate(
            ['user_id' => Auth::id()],
            ['currency' => 'KES', 'available_balance' => 0, 'locked_balance' => 0, 'status' => 'active']
        );
    }

    #[Computed]
    public function availableUnlockedGoals()
    {
        return SavingsGoal::query()
            ->where('user_id', Auth::id())
            ->where('status', 'active')
            ->get()
            ->filter(fn (SavingsGoal $goal) => $goal->isUnlocked())
            ->values();
    }

    public function withdraw(WalletService $walletService): void
{
    $this->validate();

    try {
        if ($this->source === 'available') {
            $walletService->withdrawFromAvailable(Auth::id(), (float) $this->amount);
        } else {
            $walletService->withdrawFromGoal(Auth::id(), (int) $this->goal_id, (float) $this->amount);
        }

        session()->flash('message', 'Withdrawal completed successfully.');
        $this->reset(['amount', 'goal_id']);
        $this->source = 'available';
    } catch (RuntimeException $e) {
        $this->addError('amount', $e->getMessage());
    }
}

    public function render()
    {
        return view('livewire.withdraw-funds', [
            'wallet' => $this->wallet,
            'goals' => $this->availableUnlockedGoals,
        ]);
    }
}