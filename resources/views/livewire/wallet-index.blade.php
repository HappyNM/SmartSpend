<div class="min-h-screen bg-gray-50 dark:bg-neutral-900 p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Wallet</h1>
            <p class="text-gray-600 dark:text-gray-300">Manage available and locked funds</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('wallet.deposit') }}" class="px-4 py-2 rounded-lg bg-indigo-600 text-white">Deposit</a>
            <a href="{{ route('wallet.withdraw') }}" class="px-4 py-2 rounded-lg bg-gray-800 text-white">Withdraw</a>
            <a href="{{ route('wallet.goals') }}" class="px-4 py-2 rounded-lg bg-emerald-600 text-white">Savings Goals</a>
        </div>
    </div>

    @include('livewire.wallet-partials.summary-cards', ['wallet' => $wallet])

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Recent Activity</h2>
            <a href="{{ route('wallet.transactions') }}" class="text-indigo-600 dark:text-indigo-400">View all</a>
        </div>
        @include('livewire.wallet-partials.transactions-table', ['transactions' => $transactions])
    </div>
</div>