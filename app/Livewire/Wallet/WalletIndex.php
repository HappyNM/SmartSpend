<?php

namespace App\Livewire\Wallet;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Wallet - ExpenseApp')]
class WalletIndex extends Component
{
    #[Computed]
    public function wallet(): Wallet
    {
        return Wallet::firstOrCreate(
            ['user_id' => Auth::id()],
            ['currency' => 'KES', 'available_balance' => 0, 'locked_balance' => 0, 'status' => 'active']
        );
    }

    #[Computed]
    public function recentTransactions()
    {
        return WalletTransaction::query()
            ->where('user_id', Auth::id())
            ->latest()
            ->take(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.wallet-index', [
            'wallet' => $this->wallet,
            'transactions' => $this->recentTransactions,
        ]);
    }
}