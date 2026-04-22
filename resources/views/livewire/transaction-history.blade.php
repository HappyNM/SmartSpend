<div class="min-h-screen bg-gray-50 dark:bg-neutral-900 p-6 space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Transaction History</h1>
        <p class="text-gray-600 dark:text-gray-300">Wallet activity and movement logs</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-5">
            <select wire:model.live="type" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 dark:text-gray-100">
                <option value="">All Types</option>
                <option value="deposit">Deposit</option>
                <option value="withdrawal">Withdrawal</option>
                <option value="lock">Lock</option>
                <option value="unlock">Unlock</option>
            </select>

            <select wire:model.live="status" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 dark:text-gray-100">
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="completed">Completed</option>
                <option value="failed">Failed</option>
                <option value="reversed">Reversed</option>
            </select>

            <input wire:model.live="from_date" type="date" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 dark:text-gray-100">
            <input wire:model.live="to_date" type="date" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 dark:text-gray-100">
        </div>

        @include('livewire.wallet-partials.transactions-table', ['transactions' => $transactions])

        <div class="mt-4">
            {{ $transactions->links() }}
        </div>
    </div>
</div>