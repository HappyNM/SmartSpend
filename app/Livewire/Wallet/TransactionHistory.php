<?php

namespace App\Livewire\Wallet;

use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Wallet Transactions - ExpenseApp')]
class TransactionHistory extends Component
{
    use WithPagination;

    public string $type = '';
    public string $status = '';
    public string $from_date = '';
    public string $to_date = '';

    #[Computed]
    public function transactions()
    {
        return WalletTransaction::query()
            ->where('user_id', Auth::id())
            ->when($this->type !== '', fn ($q) => $q->where('type', $this->type))
            ->when($this->status !== '', fn ($q) => $q->where('status', $this->status))
            ->when($this->from_date !== '', fn ($q) => $q->whereDate('created_at', '>=', $this->from_date))
            ->when($this->to_date !== '', fn ($q) => $q->whereDate('created_at', '<=', $this->to_date))
            ->latest()
            ->paginate(15);
    }

    public function updating($name): void
    {
        if (in_array($name, ['type', 'status', 'from_date', 'to_date'], true)) {
            $this->resetPage();
        }
    }

    public function render()
    {
        return view('livewire.transaction-history', [
            'transactions' => $this->transactions,
        ]);
    }
}