<div class="min-h-screen bg-gray-50 dark:bg-neutral-900 p-6 space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Savings Goals</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
        @include('livewire.wallet-partials.goal-form')
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-2">Move Funds to Locked Savings</h2>
    <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
        Available Balance: KES {{ number_format($this->wallet->available_balance, 2) }}
    </p>

    <form wire:submit="lockFunds" class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div>
            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Goal</label>
            <select wire:model="lock_goal_id" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 dark:text-gray-100">
                <option value="">Select goal</option>
                @foreach($goals as $goal)
                    <option value="{{ $goal->id }}">{{ $goal->name }}</option>
                @endforeach
            </select>
            @error('lock_goal_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Amount (KES)</label>
            <input wire:model="lock_amount" type="number" step="0.01" min="1" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 dark:text-gray-100">
            @error('lock_amount') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-end">
            <button type="submit" class="w-full px-4 py-2 rounded-lg bg-emerald-600 text-white font-semibold">
                Lock Funds
            </button>
        </div>
    </form>
</div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Your Goals</h2>

        <div class="space-y-3">
            @forelse($goals as $goal)
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $goal->name }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">{{ $goal->description }}</p>
                        </div>
                        <span class="text-xs px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                            {{ strtoupper($goal->status) }}
                        </span>
                    </div>
                    <div class="mt-3 text-sm text-gray-700 dark:text-gray-300">
                        Saved: KES {{ number_format($goal->current_amount, 2) }}
                        @if($goal->target_amount)
                            / KES {{ number_format($goal->target_amount, 2) }}
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-gray-500 dark:text-gray-400">No savings goals yet.</p>
            @endforelse
        </div>
    </div>
</div>