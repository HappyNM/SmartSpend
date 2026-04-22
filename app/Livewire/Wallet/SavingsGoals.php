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


#[Title('Savings Goals - ExpenseApp')]
class SavingsGoals extends Component
{
    public string $name = '';
    public string $description = '';
    public string $lock_type = 'time';
    public string $target_amount = '';
    public ?string $lock_until = null;
    public bool $allow_partial_withdrawal = true;

    protected function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'lock_type' => ['required', 'in:time,amount,time_and_amount'],
            'allow_partial_withdrawal' => ['boolean'],
        ];

        if (in_array($this->lock_type, ['amount', 'time_and_amount'], true)) {
            $rules['target_amount'] = ['required', 'numeric', 'min:1'];
        } else {
            $rules['target_amount'] = ['nullable'];
        }

        if (in_array($this->lock_type, ['time', 'time_and_amount'], true)) {
            $rules['lock_until'] = ['required', 'date', 'after:today'];
        } else {
            $rules['lock_until'] = ['nullable'];
        }

        return $rules;
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
    public function goals()
    {
        return SavingsGoal::query()
            ->where('user_id', Auth::id())
            ->latest()
            ->get();
    }

    public function createGoal(WalletService $walletService): void
{
    $this->validate();

    try {
        $walletService->createGoal(
            Auth::id(),
            $this->name,
            $this->description ?: null,
            $this->lock_type,
            $this->target_amount !== '' ? (float) $this->target_amount : null,
            $this->lock_until,
            $this->allow_partial_withdrawal
        );

        $this->reset(['name', 'description', 'target_amount', 'lock_until']);
        $this->lock_type = 'time';
        $this->allow_partial_withdrawal = true;

        session()->flash('message', 'Savings goal created successfully.');
    } catch (RuntimeException $e) {
        $this->addError('name', $e->getMessage());
    }
}

    public function render()
    {
        return view('livewire.savings-goals', [
            'goals' => $this->goals,
        ]);
    }
    
}