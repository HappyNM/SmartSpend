<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-5">
        <p class="text-sm text-gray-600 dark:text-gray-400">Available</p>
        <p class="text-2xl font-bold text-emerald-600 mt-1">KES {{ number_format($wallet->available_balance, 2) }}</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-5">
        <p class="text-sm text-gray-600 dark:text-gray-400">Locked</p>
        <p class="text-2xl font-bold text-amber-600 mt-1">KES {{ number_format($wallet->locked_balance, 2) }}</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-5">
        <p class="text-sm text-gray-600 dark:text-gray-400">Total</p>
        <p class="text-2xl font-bold text-indigo-600 mt-1">
            KES {{ number_format($wallet->available_balance + $wallet->locked_balance, 2) }}
        </p>
    </div>
</div>