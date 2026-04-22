<div class="min-h-screen bg-gray-50 dark:bg-neutral-900 p-6">
    <div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">Withdraw Funds</h1>
        <p class="text-gray-600 dark:text-gray-300 mb-6">
            Available balance: KES {{ number_format($wallet->available_balance, 2) }}
        </p>

        @if (session()->has('message'))
            <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3">
                {{ session('message') }}
            </div>
        @endif

        <form wire:submit="withdraw" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Source</label>
                <select wire:model.live="source" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 dark:text-gray-100">
                    <option value="available">Available Balance</option>
                    <option value="goal">Unlocked Savings Goal</option>
                </select>
            </div>

            @if($source === 'goal')
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Goal</label>
                    <select wire:model="goal_id" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 dark:text-gray-100">
                        <option value="">Select goal</option>
                        @foreach($goals as $goal)
                            <option value="{{ $goal->id }}">
                                {{ $goal->name }} (KES {{ number_format($goal->current_amount, 2) }})
                            </option>
                        @endforeach
                    </select>
                    @error('goal_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Amount (KES)</label>
                <input wire:model="amount" type="number" step="0.01" min="1" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 dark:text-gray-100">
                @error('amount') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="w-full px-4 py-3 rounded-lg bg-rose-600 text-white font-semibold">
                Withdraw
            </button>
        </form>
    </div>
</div>